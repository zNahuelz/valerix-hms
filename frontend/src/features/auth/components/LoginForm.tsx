import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { toast } from 'sonner';
import { Checkbox } from '@/components/ui/checkbox';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router';
import { useAuth } from '@/context/AuthContext';
import { useMemo, useState } from 'react';
import { login as loginService } from '../services/authService';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Spinner } from '@/components/ui/spinner';
import { LoginRequest } from '@/schemas/auth';
import { HTTPError } from 'ky';
import { ErrorResponse } from '@/schemas/api';
import img1 from '@/assets/images/login/img1.jpg';
import img2 from '@/assets/images/login/img2.jpg';
import img3 from '@/assets/images/login/img3.jpg';
import img4 from '@/assets/images/login/img4.jpg';
import img5 from '@/assets/images/login/img5.jpg';
import img6 from '@/assets/images/login/img6.jpg';
import img7 from '@/assets/images/login/img7.jpg';
import img8 from '@/assets/images/login/img8.jpg';
import img9 from '@/assets/images/login/img9.jpg';
import img10 from '@/assets/images/login/img10.jpg';

export default function LoginForm({ className, ...props }: React.ComponentProps<'div'>) {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { login: saveLoginToContext } = useAuth();
  const [isLoading, setIsLoading] = useState(false);

  const AUTH_IMAGES = [img1, img2, img3, img4, img5, img6, img7, img8, img9, img10];

  const randomImage = useMemo(() => {
    const randomIndex = Math.floor(Math.random() * AUTH_IMAGES.length);
    return AUTH_IMAGES[randomIndex];
  }, []);

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors },
    reset,
  } = useForm<LoginRequest>({
    resolver: zodResolver(LoginRequest),
    defaultValues: {
      username: '',
      password: '',
      rememberMe: false,
    },
  });

  const rememberMeValue = watch('rememberMe');

  const onSubmit = async (data: LoginRequest) => {
    setIsLoading(true);
    try {
      const response = await loginService(data.username, data.password);
      saveLoginToContext(response, data.rememberMe);

      toast.success(t('auth.loginSuccess'));
      navigate('/dashboard');
    } catch (error: unknown) {
      if (error instanceof HTTPError) {
        const raw = await error.response.json();
        const errorBody = ErrorResponse.parse(raw);
        const errorCode = errorBody.code || 'auth.errors.serverError';
        toast.error(
          t(errorCode, {
            lockedUntil: errorBody.lockedUntil,
            defaultValue: t('auth.errors.serverError'),
          }),
          { position: 'top-center' }
        );
        reset();
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className={cn('flex flex-col gap-6', className)} {...props}>
      <Card className='overflow-hidden p-0'>
        <CardContent className='grid p-0 md:grid-cols-2'>
          <form className='p-6 md:p-8' onSubmit={handleSubmit(onSubmit)}>
            <FieldGroup>
              <div className='flex flex-col items-center gap-2 text-center'>
                <h1 className='text-2xl font-bold'>{t('auth.welcome')}</h1>
                <p className='text-muted-foreground text-balance'>{t('auth.loginTitle')}</p>
              </div>
              <Field>
                <FieldLabel htmlFor='username'>{t('auth.username')}</FieldLabel>
                <Input
                  id='username'
                  type='text'
                  {...register('username')}
                  className={errors.username ? 'border-destructive' : ''}
                  disabled={isLoading}
                />
                {errors.username && (
                  <span className='text-xs text-destructive'>
                    {t(errors.username.message ?? '')}
                  </span>
                )}
              </Field>
              <Field>
                <div className='flex items-center'>
                  <FieldLabel htmlFor='password'>{t('auth.password')}</FieldLabel>
                  <a href='#' className='ml-auto text-sm underline-offset-2 hover:underline'>
                    {t('auth.forgotPassword')}
                  </a>
                </div>
                <Input
                  id='password'
                  type='password'
                  {...register('password')}
                  className={errors.password ? 'border-destructive' : ''}
                  disabled={isLoading}
                />
                {errors.password && (
                  <span className='text-xs text-destructive'>
                    {t(errors.password.message ?? '')}
                  </span>
                )}
              </Field>
              <Field orientation='horizontal'>
                <Checkbox
                  id='rememberMe'
                  checked={rememberMeValue}
                  onCheckedChange={(checked) => setValue('rememberMe', !!checked)}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter') {
                      e.preventDefault();
                      handleSubmit(onSubmit)();
                    }
                  }}
                ></Checkbox>
                <FieldLabel htmlFor='rememberMe'>{t('auth.rememberMe')}</FieldLabel>
              </Field>
              <Field>
                <Button type='submit' disabled={isLoading}>
                  {isLoading && <Spinner data-icon='inline-start' />}
                  {t('auth.login')}
                </Button>
              </Field>
            </FieldGroup>
          </form>
          <div className='bg-muted relative hidden md:block'>
            <img
              src={randomImage}
              alt='Login Image'
              draggable={false}
              onContextMenu={(e) => e.preventDefault()}
              className='absolute inset-0 h-full w-full object-cover dark:brightness-[0.2] dark:grayscale'
            />
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
