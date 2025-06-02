declare module "ofetch" {
  export class FetchError extends Error {
    request?: Request;
    response?: Response;
    status?: number;
    statusText?: string;
    data?: any;
    constructor(
      message: string,
      options?: {
        cause?: Error;
        url?: string;
        request?: Request;
        response?: Response;
        status?: number;
        statusText?: string;
        data?: any;
      }
    );
  }
}
