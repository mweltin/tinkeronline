import { Component, OnInit } from '@angular/core';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';
import { TokenService } from '../token.service';
import { HttpResponse, HttpHeaders } from '@angular/common/http';

@Component({
  selector: 'app-add-account',
  templateUrl: './add-account.component.html',
  styleUrls: ['./add-account.component.css']
})
export class AddAccountComponent implements OnInit {
  public addedUser: string;

  constructor(
    private registerSrv: RegisterService
  ) { }

  ngOnInit(): void {
  }

  addUser(data: any){
    this.registerSrv.addUser(data.form.value).subscribe(
      (response: HttpResponse<any> ) => {
        this.addedUser = response.body.user;
      },
      (error) => console.log(error)
    );
  }
}
