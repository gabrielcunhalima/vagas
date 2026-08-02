import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { toast } from 'sonner';

export default function FlashMessages() {
    const { flash } = usePage().props;

    useEffect(() => {
        if (flash?.success) toast.success(flash.success);
        if (flash?.info) toast.info(flash.info);
        if (flash?.error) toast.error(flash.error);
    }, [flash]);

    return null;
}
