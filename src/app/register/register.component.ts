import { Component, OnInit } from '@angular/core';
import { RegisterService } from '../register.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

  constructor(
    private registerSrv: RegisterService,
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  registerUser(data){
    this.registerSrv.registerUser(data.form.value).subscribe(
      (response) => {
        console.log(response);
        this.router.navigate(['/add-account']);
      },
      (error) => console.log(error)
    );
  }
}
