import { Helmet } from '@dr.pogodin/react-helmet';
import LoginForm from '../components/LoginForm';
import { useTranslation } from 'react-i18next';
import { useNavigate, useSearchParams } from 'react-router';
import { useEffect } from 'react';
import { toast } from 'sonner';

export default function LoginView() {
  const { t } = useTranslation();
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();

  useEffect(() => {
    const error = searchParams.get('error');
    if (error === 'expired') {
      toast.error(t('auth.errors.sessionExpired'), { position: 'top-center' });
      navigate('/', { replace: true });
    }
  }, []);
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
