import { Component, OnInit } from '@angular/core';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';
import { TokenService } from '../token.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  errorMsg: string = null;

  constructor(
    private registerSrv: RegisterService,
    private router: Router,
    private tokenSrv: TokenService
  ) { }

  ngOnInit(): void {
  }

  login(data: any): void{
    this.registerSrv.login(data.form.value).subscribe(
      (response: any) => {
        console.log(response);
        this.errorMsg = null;
        this.tokenSrv.setToken(response.token);
        this.router.navigate(['/chapter']);
        this.registerSrv.loggedIn.next(true);
      },
      (error) => {
        console.log(error);
        this.errorMsg = error.headers.get('message');
      }
    );
  }
}
