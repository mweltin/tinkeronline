import { Injectable } from '@angular/core';
import { HttpClient, HttpEvent, HttpErrorResponse, HttpEventType } from  '@angular/common/http'; 

@Injectable({
  providedIn: 'root'
})
export class FileService {

  constructor(private httpClient: HttpClient) { }

  private soltion_endpoint = 'api/solution_upload.php';

  public upload(fileName, formData) {

    return this.httpClient.post<any>(this.soltion_endpoint, formData, {  
        reportProgress: true,  
        observe: 'events'  
      });  
  }
 
}
