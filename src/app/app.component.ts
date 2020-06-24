import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { RegisterService } from './register.service';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'tinkeronline';
  isLoggedIn$: Observable<boolean>;

  constructor(
    private regServ: RegisterService
  ) { }

  ngOnInit(): void {
    this.isLoggedIn$ = this.regServ.isLoggedIn;
  }

}
