export interface UserData {
  id_user: number;
  login: string;
  email: string;
  avatar: string;
  role: Role;
}

export interface Role {
  id_role: number;
  role_name: string;
}

export interface AuthData {
  user: UserData;
  iat: string;
  iss: string;
  nbf: string;
  exp: string;
}
