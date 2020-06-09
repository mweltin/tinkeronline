import { Injectable } from '@angular/core';
import { HttpClient} from '@angular/common/http';
import { TokenService } from './token.service';

@Injectable({
  providedIn: 'root'
})
export class ChapterService {

  constructor(
    private http: HttpClient,
    private tokenSrv: TokenService
  ) { }

  private chapterEndpoint = 'api/chapter.php';
  private acceptChallengeEndpoint = 'api/accpetChallenge.php';


  getCurrentChapter(){
    return this.http.post(this.chapterEndpoint, 'None', {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
  }

  acceptChallenge( chapterId: number) {
    return this.http.post(this.acceptChallengeEndpoint, {chapterId}, {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
  }
}
