<template>
  <v-card fluid align="center" class="pa-5" min-height="97vh">
    <v-card-title class="text-h4 font-weight-bold justify-center">Wszyscy Użytkownicy</v-card-title>
    <v-card-text>
      <v-simple-table>
        <template v-slot:default>
          <thead>
            <th>Id</th>
            <th>Login</th>
            <th>Email</th>
            <th>Rola</th>
            <th>Usuń użytkownika</th>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id_user">
              <td class="text-center">{{ user.id_user }}</td>
              <td class="text-center">{{ user.login }}</td>
              <td class="text-center">{{ user.email }}</td>
              <td class="text-center" style="width: 25%">
                <v-select
                  v-model="user.role"
                  :items="roles"
                  item-text="role_name"
                  return-object
                  @change="changeRole(user)"
                ></v-select>
              </td>
              <td class="text-center"><v-btn color="red" @click="deleteUser(user)">Usuń</v-btn></td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { get, PrintError, PrintSuccess, post } from '@/lib/fetch';
import { UserData, Role } from '@/lib/types';

@Component({
  name: 'AllUsers',
})
export default class extends Vue {
  public users: UserData[] = [];
  public roles: Role[] = [];

  public async getAllUsers(): Promise<void> {
    try {
      const response = await get<{
        users: UserData[];
        roles: Role[];
      }>('http://localhost:8080/getAllUsers');
      this.users = response.users;
      this.roles = response.roles;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      console.log('catch)');
      PrintError(error);
    }
  }
  public async changeRole(user: UserData): Promise<void> {
    // if(user)
    try {
      await post<void>('http://localhost:8080/updateUser', { user: user });
      PrintSuccess();

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  public async deleteUser(user: UserData): Promise<void> {
    // console.log(user.role.role_name);
    if (user.role.role_name == 'Admin') {
      PrintError('Nie można usunąć Administratora');
      return;
    }
    try {
      await post('http://localhost:8080/deleteUser', { userId: user.id_user });
      this.users.splice(this.users.indexOf(user), 1);

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  mounted() {
    this.getAllUsers();
  }
}
</script>
