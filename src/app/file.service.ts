import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class FileService {

  constructor() { }

  public upload(fileName: string, fileContent: string): void {
    console.log(
      'file upload defined'
    )
  }
 
}
