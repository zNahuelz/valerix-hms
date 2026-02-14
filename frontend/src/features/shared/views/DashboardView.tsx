import { Helmet } from '@dr.pogodin/react-helmet';
import { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useNavigate, useSearchParams } from 'react-router';
import { toast } from 'sonner';

export default function DashboardView() {
  const { t } = useTranslation();
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();

  useEffect(() => {
    const error = searchParams.get('error');
    if (error === 'forbidden') {
      toast.error(t('auth.errors.missingPermissions'), { position: 'top-center' });
      navigate('/dashboard', { replace: true });
    }
  }, [searchParams]);
  return (
    <>
      <Helmet>
        <title>{t('views.dashboard')}</title>
      </Helmet>
      <div className='flex flex-col items-center'>
        <h1>Wip!</h1>
      </div>
    </>
  );
}
