import { useRef, useState } from 'react';
import { CloudUpload, FileText, X } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function CurriculoDropzone({ file, onChange, error, id = 'curriculo' }) {
    const inputRef = useRef(null);
    const [arrastando, setArrastando] = useState(false);

    function selecionar(files) {
        const f = files?.[0];
        if (f) onChange(f);
    }

    function limpar() {
        if (inputRef.current) inputRef.current.value = '';
        onChange(null);
    }

    return (
        <div>
            <input
                ref={inputRef}
                id={id}
                type="file"
                accept="application/pdf,.pdf"
                className="hidden"
                onChange={(e) => selecionar(e.target.files)}
            />

            {file ? (
                <div className="flex items-center gap-3 rounded-lg bg-muted/50 px-3 py-2.5 ring-1 ring-foreground/10">
                    <FileText className="size-4 shrink-0 text-primary" />
                    <span className="min-w-0 flex-1 truncate text-sm font-medium">{file.name}</span>
                    <span className="shrink-0 text-xs text-muted-foreground">
                        {(file.size / 1024 / 1024).toFixed(1)} MB
                    </span>
                    <button
                        type="button"
                        onClick={limpar}
                        className="shrink-0 cursor-pointer rounded-md p-1 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        aria-label="Remover arquivo"
                    >
                        <X className="size-4" />
                    </button>
                </div>
            ) : (
                <button
                    type="button"
                    onClick={() => inputRef.current?.click()}
                    onDragOver={(e) => {
                        e.preventDefault();
                        setArrastando(true);
                    }}
                    onDragLeave={() => setArrastando(false)}
                    onDrop={(e) => {
                        e.preventDefault();
                        setArrastando(false);
                        selecionar(e.dataTransfer.files);
                    }}
                    className={cn(
                        'flex w-full cursor-pointer flex-col items-center gap-1.5 rounded-lg border border-dashed border-input px-4 py-6 text-center transition-colors hover:border-primary/50 hover:bg-accent/40',
                        arrastando && 'border-primary bg-accent/60',
                        error && 'border-destructive',
                    )}
                >
                    <CloudUpload className="size-5 text-muted-foreground" />
                    <span className="text-sm font-medium">Arraste o PDF ou clique para selecionar</span>
                    <span className="text-xs text-muted-foreground">Somente PDF · máx. 5 MB</span>
                </button>
            )}
        </div>
    );
}
