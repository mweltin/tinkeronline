import { Component, OnInit } from '@angular/core';
import { ChapterService } from '../chapter.service';

@Component({
  selector: 'app-chapter',
  templateUrl: './chapter.component.html',
  styleUrls: ['./chapter.component.css']
})
export class ChapterComponent implements OnInit {

  public chapter: any = { title:  '', content: ''};

  constructor(
    private chapterSrv: ChapterService,
  ) {

  }

  ngOnInit(): void {

    this.chapterSrv.getCurrentChapter().subscribe(
      (res: any) => {
        this.chapter.content = res.body.chapter;
        this.chapter.title = res.body.title;
      },
      (error) => console.log(error)
    );
  }

}
