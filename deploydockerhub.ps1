$Repo = "valisama/neozama-boveda"
$Tag = "latest"

Write-Host "🚀 Building Docker image for ${Repo}:${Tag}..." -ForegroundColor Cyan
docker build -t "${Repo}:${Tag}" .

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Build successful!" -ForegroundColor Green
    Write-Host "☁️  Pushing to Docker Hub..." -ForegroundColor Cyan
    docker push "${Repo}:${Tag}"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "🎉 Successfully deployed to ${Repo}:${Tag}" -ForegroundColor Green
        Write-Host "Railway should detect the change and redeploy automatically." -ForegroundColor Yellow
    }
    else {
        Write-Host "❌ Push failed. Please check your 'docker login' status." -ForegroundColor Red
    }
}
else {
    Write-Host "❌ Build failed." -ForegroundColor Red
}
