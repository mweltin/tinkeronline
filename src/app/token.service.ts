import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TokenService {

  public token;

  constructor() { }

  setToken(token: string) {
    this.token = token;
  }

  getToken( ): string {
    return this.token;
  }

}
