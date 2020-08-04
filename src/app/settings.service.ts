import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class SettingsService {

  private settingsEndpoint = 'api/permissions.php';

  constructor(
    private http: HttpClient
    ) { }

  getSettings(){
    return this.http.get(this.settingsEndpoint);
  }
  saveSettings(formData){
    return this.http.post(this.settingsEndpoint, formData);
  }
}
