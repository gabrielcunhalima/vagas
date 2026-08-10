import PublicLayout from '@/Layouts/PublicLayout';
import PoliticaPrivacidadeConteudo from '@/components/PoliticaPrivacidadeConteudo';

export default function PoliticaPrivacidade() {
    return (
        <PublicLayout title="Política de Privacidade">
            <div className="mx-auto w-full max-w-3xl px-4 pt-12">
                <PoliticaPrivacidadeConteudo />
            </div>
        </PublicLayout>
    );
}
