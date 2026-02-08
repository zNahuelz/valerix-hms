import { useTranslation } from 'react-i18next';
export default function Test() {
  const { t } = useTranslation();
  return <div className='flex flex-col items-center'>{t('common.testString')}</div>;
}
