import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class RegisterService {

  private registerEndpoint = 'api/register.php';

  constructor(
    private http: HttpClient
  ) { }

  registerUser(data){
    return this.http.post(this.registerEndpoint, data);
  }
}
