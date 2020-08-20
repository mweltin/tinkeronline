import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { FileService } from '../file.service';

@Component({
  selector: 'app-file-upload',
  templateUrl: './file-upload.component.html',
  styleUrls: ['./file-upload.component.css']
})
export class FileUploadComponent implements OnInit {

  private fileName;

  constructor(private fb: FormBuilder, private fileSrv: FileService) { }
  
  public formGroup = this.fb.group({
    file: [null, Validators.required]
  });

  ngOnInit(): void {
  }

  public onFileChange(event) {
    const reader = new FileReader();

    if (event.target.files && event.target.files.length) {
      this.fileName = event.target.files[0].name;
      const [file] = event.target.files;
      reader.readAsDataURL(file);

      reader.onload = () => {
        this.formGroup.patchValue({
          file: reader.result
        });
      };
    }
  }

  public onSubmit(): void {
    this.fileSrv.upload(this.fileName, this.formGroup.get('file').value).subscribe(
      (data) => { console.log('file logged'); },
      (error) => { console.log(error); }
    );
  }
}