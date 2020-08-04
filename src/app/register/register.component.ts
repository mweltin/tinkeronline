import { Component, OnInit } from '@angular/core';
import { HttpResponse } from '@angular/common/http';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
})
export class RegisterComponent implements OnInit {
  errorMsg: string = null;

  constructor(
    private registerSrv: RegisterService,
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  registerUser(data){
    this.registerSrv.registerUser(data.form.value).subscribe(
      (response: HttpResponse<any> ) => {
        this.router.navigate(['/add-account']);
      },
        (error) => {
           this.errorMsg = error.headers.get('message');
        }
    );
  }
}
