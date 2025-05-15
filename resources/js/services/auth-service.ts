import { AuthPayload } from '@/types';
import { router } from '@inertiajs/vue3';
import { auth, googleProvider, githubProvider } from '@/firebase';
import { AuthProvider, UserCredential, signInWithPopup } from 'firebase/auth';

async function sendDataToCustomBackend(
    payload: AuthPayload,
    callbacks: {
        onStart?: () => void;
        onFinish?: () => void;
        onSuccess?: () => void;
        onError?: (errors: Record<string, string>) => void;
    }
): Promise<void> {
    try {
        const backendRoute = route('register');
        router.post(backendRoute, payload, {
            onStart: () => callbacks.onStart?.(),
            onFinish: () => callbacks.onFinish?.(),
            onError: (errors: any) => {
                if (typeof errors === 'object' && errors !== null && !('type' in errors)) {
                    callbacks.onError?.(errors as Record<string, string>);
                } else {
                    callbacks.onError?.({ general: 'A network or server error occurred.' });
                }
            },
            onSuccess: () => callbacks.onSuccess?.(),
        });
    } catch (error: any) {
        console.error("Error sending user data to custom backend:", error);
        callbacks.onError?.({ general: error.message || 'An unexpected error occurred.' });
        callbacks.onFinish?.();
    }
}

export async function processAndRelayFirebaseUser(
    firebaseUser: any, // Adjust type based on FirebaseUser if needed
    providerType: string,
    callbacks: {
        onStart?: () => void;
        onFinish?: () => void;
        onSuccess?: () => void;
        onError?: (errorMessage: string) => void;
    },
    formName?: string
): Promise<void> {
    callbacks.onStart?.();
    try {
        const payload: AuthPayload = {
            token: firebaseUser.token, // Token will be set by the backend initially
            displayName: firebaseUser.displayName || formName || (firebaseUser.email ? firebaseUser.email.split('@')[0] : 'User'),
            photoUrl: firebaseUser.photoURL,
            email: firebaseUser.email,
            emailVerified: firebaseUser.emailVerified,
        };

        await sendDataToCustomBackend(payload, {
            onStart: () => {},
            onFinish: () => callbacks.onFinish?.(),
            onSuccess: () => callbacks.onSuccess?.(),
            onError: (errors: Record<string, string>) => {
                callbacks.onError?.(Object.values(errors).join(' ') || 'Backend processing failed.');
            },
        });
    } catch (error: any) {
        console.error(`Error preparing data for backend relay (${providerType}):`, error);
        callbacks.onError?.(error.message || `An error occurred while preparing user data.`);
        callbacks.onFinish?.();
    }
}

export async function signInWithSocialProvider(
    providerType: 'google' | 'github',
    callbacks: {
        onStart?: () => void;
        onFinish?: () => void;
        onFirebaseSuccess?: (user: any) => void; // Adjust type based on FirebaseUser
        onFirebaseError?: (errorCode: string, errorMessage: string) => void;
        onBackendError?: (errorMessage: string) => void;
        onBackendSuccess?: () => void;
    },
    token: string
): Promise<void> {
    callbacks.onStart?.();
    const provider: AuthProvider = providerType === 'google' ? googleProvider : githubProvider;
    try {
        const result: UserCredential = await signInWithPopup(auth, provider);
        callbacks.onFirebaseSuccess?.(result.user);

        await processAndRelayFirebaseUser({...result.user, token: token}, providerType, {
            onStart: () => {},
            onFinish: () => callbacks.onFinish?.(),
            onSuccess: () => callbacks.onBackendSuccess?.(),
            onError: (msg) => callbacks.onBackendError?.(msg),
        });
    } catch (error: any) {
        callbacks.onFirebaseError?.(error.code, error.message);
        callbacks.onFinish?.();
    }
}
