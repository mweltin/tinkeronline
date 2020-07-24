import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { HttpResponse } from '@angular/common/http';
import { SettingsService } from '../settings.service';
import { TokenService } from '../token.service';

@Component({
  selector: 'app-setting-form',
  templateUrl: './setting-form.component.html',
  styleUrls: ['./setting-form.component.css']
})
export class SettingFormComponent implements OnInit {

  errorMsg;

  constructor(
    private fb: FormBuilder,
    private settingsSrv: SettingsService,
    private tokenSrv: TokenService
  ) {}

  settingForm = this.fb.group({
    username: [''],
    email: [''],
    billing_info: [''],
  });

  ngOnInit(): void {
    this.settingsSrv.getSettings().subscribe(
      (response: HttpResponse<any> ) => {
        this.tokenSrv.setToken(response.headers.get('Authorzie'));
        this.settingForm.controls.username.setValue(response.body.user_settings.username);
        this.settingForm.controls.email.setValue(response.body.user_settings.email);
        this.settingForm.controls.billing_info.setValue(response.body.user_settings.billing_info);
      },
        (error) => {
           this.errorMsg = error.headers.get('message');
        }
    );
  }

  onSubmit(){
    return true;
  }
}
