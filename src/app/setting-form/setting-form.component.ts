import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormArray, FormControl } from '@angular/forms';
import { HttpResponse } from '@angular/common/http';
import { SettingsService } from '../settings.service';
import { TokenService } from '../token.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Setting } from '../setting';

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
    private modalService: NgbModal
  ) {}

  settingForm = this.fb.group({
    username: [''],
    email: [''],
    billing_info: [''],
    children: this.fb.array([
    ]),
    asset_approval: this.fb.array([])
  });

  permissions = [];
  assetMetaData = [];

  get children(){
    return this.settingForm.get('children') as FormArray;
  }

  get asset_approval(){
    return this.settingForm.get('asset_approval') as FormArray;
  }

  ngOnInit(): void {
    this.settingsSrv.getSettings().subscribe(
      (response: Setting ) => {
        this.settingForm.controls.username.setValue(response.user_settings.username);
        this.settingForm.controls.email.setValue(response.user_settings.email);
        this.settingForm.controls.billing_info.setValue(response.user_settings.billing_info);
        for (const field of response.child_settings ){
            const childGrp = new FormGroup({});
            const childNameCtrl = new FormControl();
            childNameCtrl.setValue(field.username);
            const childEmailCtrl = new FormControl();
            childEmailCtrl.setValue(field.email);
            const childIdCtrl = new FormControl();
            childIdCtrl.setValue(field.account_id);
            childGrp.addControl('name', childNameCtrl);
            childGrp.addControl('email', childEmailCtrl);
            childGrp.addControl('id', childIdCtrl);
            const childPermGrp = new FormGroup({});
            for (const perm of field.permissions){
              const childPerm = new FormControl();
              const truth = perm.has_permission === "true";
              childPerm.setValue(truth);
              childPermGrp.addControl(perm.name, childPerm);
            }
            childGrp.addControl('perms', childPermGrp);
            this.children.push(childGrp);
          }

        for ( const asset of response.assets_to_approve){
          const assetGrp = new FormGroup({});
          const assetCtrl = new FormControl();
          assetCtrl.setValue(false);
          assetGrp.addControl(asset.asset_name, assetCtrl);

          this.asset_approval.push(assetGrp);
          this.assetMetaData.push(asset);
        }

        this.permissions = response.child_settings[0].permissions.map( x => x.name);
      },
(error) => {
           this.errorMsg = error.headers.get('message');
        }
    );
  }

onSubmit(){
    this.settingsSrv.saveSettings(this.settingForm.value).subscribe(
      (resp) => {
        this.modalService.dismissAll();
      }
    );
  }
}
