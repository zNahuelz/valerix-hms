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
import { useState } from 'react';
import { loginSchema, type LoginSchema } from '@/schemas/auth';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { loginRequest } from '../services/authService';
import { Spinner } from '@/components/ui/spinner';

export default function LoginForm({ className, ...props }: React.ComponentProps<'div'>) {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { login } = useAuth();
  const [isLoading, setIsLoading] = useState(false);

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors },
  } = useForm<LoginSchema>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      username: '',
      password: '',
      rememberMe: false,
    },
  });

  const rememberMeValue = watch('rememberMe');

  const onSubmit = async (data: LoginSchema) => {
    setIsLoading(true);
    try {
      const response = await loginRequest(data.username, data.password);

      login(response, data.rememberMe);

      toast.success(t('auth.loginSuccess'));

      navigate('/dashboard');
    } catch (error: any) {
      toast.error(t('auth.errorInvalid'));
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
                  <span className='text-xs text-destructive'>{errors.username.message}</span>
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
                  <span className='text-xs text-destructive'>{errors.password.message}</span>
                )}
              </Field>
              <Field orientation='horizontal'>
                <Checkbox
                  id='rememberMe'
                  checked={rememberMeValue}
                  onCheckedChange={(checked) => setValue('rememberMe', !!checked)}
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
              src='/placeholder.svg'
              alt='Image'
              className='absolute inset-0 h-full w-full object-cover dark:brightness-[0.2] dark:grayscale'
            />
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
