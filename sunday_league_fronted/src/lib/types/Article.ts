import { UserData } from './UserData';

export interface Article {
  id_article: number;
  title: string;
  subtitle: string;
  text: string;
  author?: UserData;
  photo: string;
  date: string;
  comments: Komentarz[];
}

export interface League {
  id_league: number;
  league_name: string;
}

export interface Komentarz {
  id: number;
  user_name: string;
  user_avatar: string;
  comment_text: string;
  date: string;
}
