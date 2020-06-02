import { Injectable } from '@angular/core';
import { HttpClient} from '@angular/common/http';
import { TokenService } from './token.service';

@Injectable({
  providedIn: 'root'
})
export class ChapterService {

  private chapterEndpoint = 'api/chapter.php';

  constructor(
    private http: HttpClient,
    private tokenSrv: TokenService
  ) { }

  getCurrentChapter(){
    return this.http.post(this.chapterEndpoint, 'None', {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
  }

}
