# Script untuk membersihkan semua merge conflict markers
# Script ini akan menggunakan versi "incoming" (setelah =======) dan menghapus versi "HEAD"

Write-Host "Mencari file dengan merge conflicts..." -ForegroundColor Yellow

# Cari semua file yang memiliki merge conflict markers
$files = Get-ChildItem -Path "c:\laragon\www\ProjectOmahBan" -Recurse -File -Include *.php,*.blade.php,*.js,*.css,*.scss,*.json | 
    Where-Object { $_.FullName -notmatch "\\node_modules\\" -and $_.FullName -notmatch "\\vendor\\" -and $_.FullName -notmatch "\\storage\\framework\\views\\" } |
    Select-String -Pattern "<<<<<<< HEAD" |
    Select-Object -ExpandProperty Path -Unique

Write-Host "Ditemukan $($files.Count) file dengan merge conflicts" -ForegroundColor Cyan

foreach ($file in $files) {
    Write-Host "Memproses: $file" -ForegroundColor Green
    
    $content = Get-Content $file -Raw
    
    # Pola regex untuk menghapus bagian HEAD dan mempertahankan bagian incoming
    # Pola: <<<<<<< HEAD\r?\n(.*?)\r?\n=======\r?\n(.*?)\r?\n>>>>>>> [^\r\n]+
    $pattern = '<<<<<<< HEAD\r?\n([\s\S]*?)\r?\n=======\r?\n([\s\S]*?)\r?\n>>>>>>> [^\r\n]+'
    
    # Ganti dengan bagian incoming (group 2)
    $newContent = $content -replace $pattern, '$2'
    
    # Simpan file
    Set-Content -Path $file -Value $newContent -NoNewline
    
    Write-Host "  ✓ Selesai" -ForegroundColor DarkGreen
}

Write-Host "`nSemua merge conflicts telah diselesaikan!" -ForegroundColor Green
$totalFiles = $files.Count
Write-Host "Total file yang diperbaiki: $totalFiles" -ForegroundColor Cyan
