export interface TeamTable {
  team_name: string;
  games: number;
  points: number;
  wins: number;
  draws: number;
  loses: number;
  goal_plus: number;
  goal_minus: number;
  goal_plus_minus: number;
}

export interface CloseGame {
  id: number;
  team_a: string;
  goals_a: number;
  team_b: string;
  goals_b: number;
  league_name: string;
  adrdress_a: string;
  lat_a: number;
  long_a: number;
  date: string;
  distance: number;
}

export interface Schedule {
  id: number;
  team_a: string;
  goals_a: number;
  team_b: string;
  goals_b: number;
  date: string;
  lat_a: number;
  long_a: number;
}
