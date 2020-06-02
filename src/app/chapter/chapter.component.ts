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
      (res) => {
        this.chapter.content = 'klsdjfldskfj';
        this.chapter.title = 'fingers crossed';
      },
      (error) => console.log(error)
    );
  }

}
