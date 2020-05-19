import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class RegisterService {

  private registerEndpoint = 'api/register.php';
  private addUserEndpoint = 'api/addUser.php';
  private loginEndpoint = 'api/login.php';

  constructor(
    private http: HttpClient
  ) { }

  registerUser(data: any){
    return this.http.post(this.registerEndpoint, data);
  }

  addUser(data: any){
    return this.http.post(this.addUserEndpoint, data);
  }

  login(data: any){
    return this.http.post(this.loginEndpoint, data);
  }
}
