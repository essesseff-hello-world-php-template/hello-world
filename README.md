# hello-world Application (PHP / Slim)

PHP [Slim Framework](https://www.slimframework.com/) implementation with trunk-based development, CI/CD through source → GitHub Actions → GHCR, and GitOps-driven deployments via Argo CD to Kubernetes (see related repositories), with *optional* process orchestration, reporting, etc. via the [essesseff DevOps platform](https://essesseff.com) available on a per essesseff app subscription.

Setup **GitHub → Argo CD → K8s** in *less than 5 minutes* with the [essesseff onboarding utility](https://github.com/essesseff/essesseff-onboarding-utility) *(absolutely free -- no essesseff subscription required)*.

*Please Note:*

*essesseff™ is an independent DevOps ALM PaaS-as-SaaS and is in no way affiliated with, endorsed by, sponsored by, or otherwise connected to GitHub® or The Linux Foundation®.*

*essesseff™ and the essesseff™ logo design are trademarks of essesseff LLC.*

*GITHUB®, the GITHUB® logo design and the INVERTOCAT logo design are trademarks of GitHub, Inc., registered in the United States and other countries.*

*Argo®, Helm®, Kubernetes® and K8s® are registered trademarks of The Linux Foundation.*

## Tech Stack

| This PHP template |
|---|
| PHP 8.3 |
| Slim Framework 4 |
| PHP built-in server (dev/container) |
| Composer 2 |
| composer.json |

> **Note on `/docs` and `/redoc`:** Slim does not provide this out of the box. If you need OpenAPI/Swagger UI, add [zircote/swagger-php](https://github.com/zircote/swagger-php) to your `composer.json` and serve the spec from a `/docs` route.

## essesseff App GitHub Repository Structure

* Source: [hello-world (this repo)](https://github.com/essesseff-hello-world-php-template/hello-world)
* Helm Config DEV: [hello-world-config-dev](https://github.com/essesseff-hello-world-php-template/hello-world-config-dev)
* Helm Config QA: [hello-world-config-qa](https://github.com/essesseff-hello-world-php-template/hello-world-config-qa)
* Helm Config STAGING: [hello-world-config-staging](https://github.com/essesseff-hello-world-php-template/hello-world-config-staging)
* Helm Config PROD: [hello-world-config-prod](https://github.com/essesseff-hello-world-php-template/hello-world-config-prod)
* Argo CD Config DEV: [hello-world-argocd-dev](https://github.com/essesseff-hello-world-php-template/hello-world-argocd-dev)
* Argo CD Config QA: [hello-world-argocd-qa](https://github.com/essesseff-hello-world-php-template/hello-world-argocd-qa)
* Argo CD Config STAGING: [hello-world-argocd-staging](https://github.com/essesseff-hello-world-php-template/hello-world-argocd-staging)
* Argo CD Config PROD: [hello-world-argocd-prod](https://github.com/essesseff-hello-world-php-template/hello-world-argocd-prod)

## Develop, Build and Deploy

* **Branch Strategy**: Single `main` branch (trunk-based)
* **Auto-Build**: GitHub Actions image build runs on code push to `main` branch
* **Auto-Deploy**: DEV CI/CD deployment subsequent to successful image build (via [essesseff](https://essesseff.com) deployment orchestration)
* **ClickOps Promote/Deploy/Re-Deploy/Rollback**: DEV, QA, STAGING, PROD (via [essesseff](https://essesseff.com) UX)
* **GitOps Deploy**: DEV, QA, STAGING, PROD (managed by Argo CD by updating config-env values.yaml)
* **API Promote/Deploy**: DEV, QA, STAGING, PROD (via [essesseff public API](https://www.essesseff.com/docs/api))
* **K8s Namespace**: this template assumes a mapping of GitHub organization ~ K8s namespace i.e. string replace `essesseff-hello-world-php-template` with your K8s namespace

## Development Workflow

```bash
# 1. Create feature branch
git checkout -b feature/my-feature

# 2. Make changes and commit
git commit -am "Add feature"

# 3. Push and create PR
git push origin feature/my-feature

# 4. After review, merge to main
# This triggers automatic build

# 5. *If an essesseff subscriber*, upon successful build completion, Helm config-dev values.yaml
#    will be automatically updated with the newly built image tag, triggering Argo CD DEV
#    to trigger automated deployment to DEV Kubernetes.

# 6. Use essesseff UI for promotions:
#    - Developer declares Release Candidate
#    - QA accepts RC → deploys to QA (or alternatively rejects the promotion of the RC to QA)
#    - QA marks as Stable (or alternatively rejects the promotion to Stable)
#    - Release Engineer deploys from Stable Release to STAGING/PROD
```

## Local Development

```bash
# Install dependencies
composer install

# Run locally using PHP built-in server
php -S 0.0.0.0:8080 -t . index.php

# Or set a custom port via environment variable
PORT=9090 php -S 0.0.0.0:9090 -t . index.php
```

## Docker

```bash
# Build container
docker build -t hello-world-php:local .

# Run container
docker run -p 8080:8080 hello-world-php:local
```

## Endpoints

* `/` - Main page with version information (HTML)
* `/health` - Health check (returns JSON)
* `/ready` - Readiness check (returns JSON)

## Environment Variables

* `PORT` - Port to run the application on (default: `8080`)
* `ENVIRONMENT` - Deployment environment name (default: `unknown`)
* `VERSION` - Application version, typically injected at deploy time (default: `unknown`)

## Project Structure

```
.
├── index.php           # Main Slim application (entry point)
├── composer.json       # PHP dependencies
├── Dockerfile          # Container definition
├── semver.txt          # Version tracking
├── .gitignore          # Git ignore patterns
├── .dockerignore       # Docker ignore patterns
└── README.md           # This file
```

## Testing

```bash
# Test health endpoint
curl http://localhost:8080/health

# Test readiness endpoint
curl http://localhost:8080/ready

# Test main page
curl http://localhost:8080/
```

## Deployment

The application is built automatically and ready to deploy to DEV after changes are merged to `main` and the build succeeds. If an essesseff subscriber, essesseff updates the Helm config-dev `values.yaml` with the newly built image tag, triggering Argo CD DEV to deploy to Kubernetes DEV. Promotion to QA, STAGING, and PROD is managed through the essesseff platform.

### Container Image Tags

Container images are tagged with the format:

```
{semver}-{git-hash}-{timestamp}
```

Example: `1.0.0-a1b2c3d-20231201T120000Z`

## CI/CD

GitHub Actions workflow (`.github/workflows/build.yml`) handles:

* Building the Docker image
* Pushing to GitHub Container Registry
* Generating build metadata
* Triggering essesseff deployment to DEV

## Health Checks

The `/health` and `/ready` endpoints can be used by:

* Kubernetes liveness/readiness probes
* Load balancers
* Monitoring systems

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose, and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages, or other liability, whether in an action of contract, tort, or otherwise, arising from, out of, or in connection with the software or the use or other dealings in the software.

Use at your own risk. The maintainers of this project make no guarantees about its functionality, security, or suitability for any purpose.
