import { Component, OnInit } from '@angular/core';
import { ChapterService } from '../chapter.service';
import { TokenService } from '../token.service';

@Component({
  selector: 'app-chapter',
  templateUrl: './chapter.component.html',
  styleUrls: ['./chapter.component.css']
})
export class ChapterComponent implements OnInit {

  public chapter: any = { title:  '', content: ''};
  public accpeted = false;
  public solved = false;
  public chapterId: number;

  constructor(
    private chapterSrv: ChapterService,
    private tokenSrv: TokenService
  ) {

  }

  ngOnInit(): void {

    this.chapterSrv.getCurrentChapter().subscribe(
      (res: any) => {
        this.chapter.content = res.body.chapter;
        this.chapter.title = res.body.title;
        this.solved = res.body.solved;
        this.accpeted = res.body.accepted;
        this.chapterId = res.body.chapter_id;
        this.tokenSrv.setToken(res.headers.get('Authorzie'));

      },
      (error) => console.log(error)
    );
  }

  acceptChallenge(): void {
    this.chapterSrv.acceptChallenge(this.chapterId).subscribe(
      (res: any) => {
        this.accpeted = res.body.accepted;
      },
      (error) => console.log(error)
    );
  }
}

