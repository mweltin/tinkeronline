import { Component, OnInit } from '@angular/core';
import { ChapterService } from '../chapter.service';
import { FileUploadComponent } from '../file-upload/file-upload.component'

@Component({
  selector: 'app-chapter',
  templateUrl: './chapter.component.html',
  styleUrls: ['./chapter.component.css']
})
export class ChapterComponent implements OnInit {

  public chapter: any = { title:  '', content: ''};
  public accpeted = 0;
  public solved = false;
  public chapterId: number;
  public showForm = false;

  constructor(
    private chapterSrv: ChapterService,
  ) {

  }

  ngOnInit(): void {

    this.chapterSrv.getCurrentChapter().subscribe(
      (res: any) => {
        this.chapter.content = res.chapter;
        this.chapter.title = res.title;
        this.solved = res.solved;
        this.accpeted = res.accepted;
        this.chapterId = res.chapter_id;
      },
      (error) => console.log(error)
    );

  }

  acceptChallenge(): void {
    this.chapterSrv.acceptChallenge(this.chapterId).subscribe(
      (res: any) => {
        this.accpeted = res.accepted;
      },
      (error) => console.log(error)
    );
  }

  showUploadForm(): void {
    this.showForm = true;
  }
}

