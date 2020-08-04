import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ChapterComponent } from './chapter/chapter.component';
import { AddAccountComponent } from './add-account/add-account.component';
import { MustMatchDirective } from './register/must-match.directive';
import { EmailMatchDirective } from './register/email-match.directive';
import { SettingsComponent } from './settings/settings.component';
import { SettingFormComponent } from './setting-form/setting-form.component';
import { JwtInterceptor } from './jwt_interceptor';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    ChapterComponent,
    AddAccountComponent,
    MustMatchDirective,
    EmailMatchDirective,
    SettingsComponent,
    SettingFormComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    HttpClientModule
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
