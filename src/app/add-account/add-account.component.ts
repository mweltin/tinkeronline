import { Component, OnInit } from '@angular/core';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-add-account',
  templateUrl: './add-account.component.html',
  styleUrls: ['./add-account.component.css']
})
export class AddAccountComponent implements OnInit {
  public addedUser: string;

  constructor(
    private registerSrv: RegisterService,
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  addUser(data: any){
    this.registerSrv.addUseUser(data.form.value).subscribe(
      (response) => {
        console.log('got here');
        this.addedUser = 'bob';
      },
      (error) => console.log(error)
    );
  }
}
