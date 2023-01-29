export class FetchError extends Error {
  public errMsg: any;
  public errCode: string;

  constructor(errMsg: string, errCode: string) {
    super();
    this.errMsg = errMsg;
    this.errCode = errCode;
  }
}
