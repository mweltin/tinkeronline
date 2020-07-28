import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormArray, FormControl } from '@angular/forms';
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
    children: this.fb.array([
    ])
  });

  permissions = [];

  get children(){
    return this.settingForm.get('children') as FormArray;
  }


  ngOnInit(): void {
    this.settingsSrv.getSettings().subscribe(
      (response: HttpResponse<any> ) => {
        this.tokenSrv.setToken(response.headers.get('Authorzie'));
        this.settingForm.controls.username.setValue(response.body.user_settings.username);
        this.settingForm.controls.email.setValue(response.body.user_settings.email);
        this.settingForm.controls.billing_info.setValue(response.body.user_settings.billing_info);
        for (const field of response.body.child_settings ){
            const childGrp = new FormGroup({});
            const childNameCtrl = new FormControl();
            childNameCtrl.setValue(field.username);
            const childEmailCtrl = new FormControl();
            childEmailCtrl.setValue(field.email);
            childGrp.addControl('name', childNameCtrl);
            childGrp.addControl('email', childEmailCtrl);
            const childPermGrp = new FormGroup({});
            for (const perm of field.permissions){
              const childPerm = new FormControl();
              childPerm.setValue(Boolean(perm.has_permission == 'true'));
              childPermGrp.addControl(perm.name, childPerm);
            }
            childGrp.addControl('perms', childPermGrp);
            this.children.push(childGrp);
          }
        this.permissions = response.body.child_settings[0].permissions.map( x => x.name);
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
