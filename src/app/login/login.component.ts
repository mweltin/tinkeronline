import { Component, OnInit } from '@angular/core';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  errorMsg: string = null;

  constructor(
    private registerSrv: RegisterService,
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  login(data: any): void{
    this.registerSrv.login(data.form.value).subscribe(
      (response: any) => {
        this.registerSrv.loggedIn$.next(true);
        this.errorMsg = null;
        this.router.navigate(['/chapter']);
      },
      (error) => {
        console.log(error);
        this.errorMsg = error.headers.get('message');
      }
    );
  }

}
