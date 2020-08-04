import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpHandler, HttpRequest, HttpEvent, HttpResponse } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, filter } from 'rxjs/operators';
import { TokenService } from './token.service';
import { RegisterService } from './register.service';

@Injectable()
export class JwtInterceptor implements HttpInterceptor {
    constructor(
        private registerSrv: RegisterService,
        private tokenSrv: TokenService
      ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    let isLoggedIn: boolean;
    this.registerSrv.isLoggedIn.subscribe( val => isLoggedIn = val );
    let customReqHeaders = request.headers;
    console.log('request interceptor working');
    let token = this.tokenSrv.getToken();
    if ( isLoggedIn && token ){
        customReqHeaders = customReqHeaders.append( 'Authorzie', token );
    }
    request = request.clone({headers: customReqHeaders});
    // Then we return an Observable that will run the request
    // or pass it to the next interceptor if any
    return next.handle(request).pipe(
        map( resp => {
            if (resp instanceof HttpResponse) { 
                token = resp.headers.get('authorzie');
                this.tokenSrv.setToken(token);
                return  resp.clone();
            }
        })
    )
  }
}
