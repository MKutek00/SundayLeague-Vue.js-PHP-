import { AuthData, UserData } from '../types';
import { FetchError } from './FetchError';
import { HttpMethods } from './HttpMethods';
import User from '@/store/modules/User';
import { getModule } from 'vuex-module-decorators';

async function handleResponse<T>(response: Response): Promise<T> {
  const data = await response.json();

  if (!response.ok) {
    const errCode = data?.errorCode ? `API_${data?.errorCode}` : `HTTP_${response.status}`;
    const errMsg = data?.errorMsg ?? response.statusText;
    throw new FetchError(errMsg, errCode);
  }
  if (data.error) {
    throw new Error(data.error);
  }

  return data as T;
}

function authHeader() {
  // return authorization header with jwt token
  const jwt = localStorage.getItem('sundayleague.jwt');

  if (jwt) {
    return { Authorization: 'Bearer ' + jwt };
  }
}

export function jwtParse(token: string | null): AuthData | null {
  if (!token) return null;

  const data = token.split('.')[1];
  if (!data) return null;
  try {
    const rawData = atob(data);
    return { ...JSON.parse(rawData) };
  } catch (error) {
    return null;
  }
}

async function fetchData<T>(url: string, data: unknown, method: HttpMethods = 'POST'): Promise<T> {
  const request: RequestInit = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': '*',
      ...authHeader(),
    },
  };
  if (data) {
    request.body = JSON.stringify(data);
  }
  const response = await fetch(url, request);
  return handleResponse(response);
}
// Authorization: `Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2NzA1MzgyMjksImlzcyI6InlvdXIuZG9tYWluLm5hbWUiLCJuYmYiOjE2NzA1MzgyMjksImV4cCI6MTY3MDUzODU4OSwidXNlck5hbWUiOiJ1c2VybmFtZSJ9.Drg9KLVtQdTPHwFR_I14H-SG5aK51UM_tdNXZP7iTaoPZ_KK15kaH4ju9YJpaEwluO53BaCam9wCQTG5UiXlpg`,
type FetchMethod = <T>(url: string, data: unknown) => Promise<T>;

export const get: <T>(url: string) => Promise<T> = (url: string) => fetchData(url, null, 'GET');
export const post: FetchMethod = (url: string, data: unknown) => fetchData(url, data, 'POST');
export const patch: FetchMethod = (url: string, data: unknown) => fetchData(url, data, 'PATCH');
export const put: FetchMethod = (url: string, data: unknown) => fetchData(url, data, 'PUT');
export const del: FetchMethod = (url: string, data: unknown) => fetchData(url, data, 'DELETE');
export const head: FetchMethod = (url: string, data: unknown) => fetchData(url, data, 'HEAD');
