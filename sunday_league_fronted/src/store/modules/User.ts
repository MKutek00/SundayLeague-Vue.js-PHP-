/* eslint-disable @typescript-eslint/no-explicit-any */
import { PrintError } from '@/lib/fetch';
import { jwtParse, post } from '@/lib/fetch/helpers';
import { UserData } from '@/lib/types';
import Swal from 'sweetalert2';
import { Module, VuexModule, Mutation, Action } from 'vuex-module-decorators';
import Store from '../index';

@Module({
  dynamic: true,
  store: Store,
  name: 'User',
  namespaced: true,
})
export default class UserModule extends VuexModule {
  public isLoggedIn: boolean = false;
  public userData: UserData | null = null;

  @Action
  public async loginUser(user: { login: string; password: string }): Promise<void> {
    let token: string = '';
    try {
      token = await post<string>('http://localhost:8080/loginUser', user);
      Swal.fire({
        icon: 'success',
        title: 'Zalogowano',
        timer: 1500,
        showConfirmButton: false,
      });
      localStorage.setItem('sundayleague.jwt', token);
      console.log(token);
    } catch (error: any) {
      PrintError(error);
    }

    const parsedJWT = jwtParse(token);
    if (parsedJWT?.user != undefined) this.setUserData(parsedJWT?.user);
  }

  @Action
  public logout(): void {
    this.setUserData(null);
    localStorage.removeItem('sundayleague.jwt');
    Swal.fire({
      icon: 'success',
      title: 'Wylogowano pomyślnie',
      timer: 1500,
      showConfirmButton: false,
    });
  }

  @Action
  public checkStorage(): void {
    const parsedJWT = jwtParse(localStorage.getItem('sundayleague.jwt'));
    if (parsedJWT != null && Number(parsedJWT.exp) > Date.now() / 1000) {
      this.setUserData(parsedJWT.user);
    }
  }

  @Mutation
  public setUserData(userData: UserData | null): void {
    console.log(userData);
    this.userData = userData;
    this.isLoggedIn = !!userData;
  }
}
