export type EntityResponse<R extends string, T> = {
  message: string;
} & {
  [K in R]: T;
};
