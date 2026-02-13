import { DateTime } from 'luxon';
export function formatAsDatetime(date: string | any) {
  return date
    ? DateTime.fromISO(date)
        .setZone('America/Lima')
        .setLocale('es')
        .toFormat('dd LLL yyyy hh:mm a')
        .replace('a. m.', 'AM')
        .replace('p. m.', 'PM')
    : '';
}

export function formatAsDate(date: string | any) {
  return date
    ? DateTime.fromISO(date).setZone('America/Lima').setLocale('es').toFormat('dd LLL yyyy')
    : '';
}

export function formatAsTime(date: string | any) {
  return date
    ? DateTime.fromISO(date)
        .setZone('America/Lima')
        .setLocale('es')
        .toFormat('hh:mm a')
        .replace('a. m.', 'AM')
        .replace('p. m.', 'PM')
    : '';
}
