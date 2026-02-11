import { Helmet } from '@dr.pogodin/react-helmet';
import LoginForm from '../components/LoginForm';
import { useTranslation } from 'react-i18next';

export default function LoginView() {
  const { t } = useTranslation();
  return (
    <>
      <Helmet>
        <title>{t('views.login')}</title>
      </Helmet>
      <div className='bg-muted flex min-h-svh flex-col items-center justify-center p-6 md:p-10'>
        <div className='w-full max-w-sm md:max-w-4xl'>
          <LoginForm />
        </div>
      </div>
    </>
  );
}
