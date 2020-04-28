import { Component, OnInit } from '@angular/core';
import { RegisterService } from '../register.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

  constructor(
    private registerSrv: RegisterService
  ) { }

  ngOnInit(): void {
  }

  registerUser(data){
    this.registerSrv.registerUser(data.form.value).subscribe(
      (response) => console.log(response),
      (error) => console.log(error)
    );
  }
}
