import { Component, OnInit } from '@angular/core';
import { HttpResponse, HttpHeaders } from '@angular/common/http';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';
import { TokenService } from '../token.service';



@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
})
export class RegisterComponent implements OnInit {
  errorMsg: string = null;

  constructor(
    private registerSrv: RegisterService,
    private router: Router,
    private tokenSrv: TokenService
  ) { }

  ngOnInit(): void {
  }

  registerUser(data){
    this.registerSrv.registerUser(data.form.value).subscribe(
      (response: HttpResponse<any> ) => {
        this.tokenSrv.setToken(response.headers.get('Authorzie'));
        this.router.navigate(['/add-account']);
      },
        (error) => {
           this.errorMsg = error.headers.get('message');
        }
    );
  }
}
