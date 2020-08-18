import { Injectable } from '@angular/core';
import { HttpClient} from '@angular/common/http';
import { Observable, Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ChapterService {

  constructor(
    private http: HttpClient
  ) { }

  private chapterEndpoint = 'api/chapter.php';
  private acceptChallengeEndpoint = 'api/accpetChallenge.php';


  getCurrentChapter(){
    return this.http.post(this.chapterEndpoint, 'None');
  }

  acceptChallenge( chapterId: number) {
    return this.http.post(this.acceptChallengeEndpoint, {chapterId});
  }

}
