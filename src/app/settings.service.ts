import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { TokenService } from './token.service';

@Injectable({
  providedIn: 'root'
})
export class SettingsService {

  private settingsEndpoint = 'api/permissions.php';

  constructor(
    private http: HttpClient,
    private tokenSrv: TokenService
    ) { }

  getSettings(){
    return this.http.get(this.settingsEndpoint, {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
  }
  saveSettings(formData){
    return this.http.post(this.settingsEndpoint, formData, {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
  }
}
