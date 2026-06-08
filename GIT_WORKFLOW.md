# Git Workflow - VETRA Project

## 📦 Repository Information

**GitHub URL:** https://github.com/mahayasa78/VETRA-WEB.git  
**Branch:** main  
**Remote:** origin

---

## 🚀 Initial Setup (Already Done)

```bash
# 1. Initialize git (already done)
git init

# 2. Add remote
git remote add origin https://github.com/mahayasa78/VETRA-WEB.git

# 3. Set remote URL (if changed)
git remote set-url origin https://github.com/mahayasa78/VETRA-WEB.git
```

---

## 📤 Pushing Changes

### First Time Push
```bash
git push -u origin main
```

### Subsequent Pushes
```bash
# 1. Check status
git status

# 2. Add files
git add .

# 3. Commit
git commit -m "Your commit message"

# 4. Push
git push
```

---

## 🔄 Daily Workflow

### 1. Before Starting Work
```bash
# Pull latest changes
git pull origin main
```

### 2. After Making Changes
```bash
# Check what changed
git status

# See detailed changes
git diff

# Add all changes
git add .

# Or add specific files
git add path/to/file.php

# Commit with meaningful message
git commit -m "feat: add new feature"

# Push to GitHub
git push
```

---

## 📝 Commit Message Convention

### Format:
```
<type>: <description>

[optional body]
```

### Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

### Examples:
```bash
git commit -m "feat: add email notification system"
git commit -m "fix: resolve clinic bookings not loading issue"
git commit -m "docs: add complete API documentation"
git commit -m "refactor: improve error handling in controllers"
```

---

## 🌿 Branching Strategy

### Create New Branch
```bash
# For new feature
git checkout -b feature/feature-name

# For bug fix
git checkout -b fix/bug-name

# For hotfix
git checkout -b hotfix/issue-name
```

### Switch Branch
```bash
git checkout main
git checkout feature/feature-name
```

### Merge Branch
```bash
# Switch to main
git checkout main

# Merge feature branch
git merge feature/feature-name

# Push merged changes
git push
```

### Delete Branch
```bash
# Delete local branch
git branch -d feature/feature-name

# Delete remote branch
git push origin --delete feature/feature-name
```

---

## 🔍 Useful Commands

### Check Status
```bash
git status
```

### View Commit History
```bash
git log --oneline
git log --graph --oneline --all
```

### View Changes
```bash
# Unstaged changes
git diff

# Staged changes
git diff --cached

# Changes in specific file
git diff path/to/file.php
```

### Undo Changes
```bash
# Unstage file (keep changes)
git restore --staged file.php

# Discard changes in file
git restore file.php

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1
```

### View Remote Info
```bash
git remote -v
git remote show origin
```

---

## 🔐 Authentication

### SSH (Recommended for frequent use)
```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your-email@example.com"

# Copy public key
cat ~/.ssh/id_ed25519.pub

# Add to GitHub: Settings → SSH and GPG keys → New SSH key

# Test connection
ssh -T git@github.com

# Change remote to SSH
git remote set-url origin git@github.com:mahayasa78/VETRA-WEB.git
```

### HTTPS (Current)
- Browser authentication window akan muncul
- Login dengan GitHub account
- Token akan tersimpan untuk future pushes

---

## 📋 Common Workflows

### Workflow 1: Quick Update
```bash
git add .
git commit -m "update: quick fixes"
git push
```

### Workflow 2: Feature Development
```bash
# Create feature branch
git checkout -b feature/new-api-endpoint

# Make changes...

# Commit changes
git add .
git commit -m "feat: add new API endpoint"

# Push feature branch
git push -u origin feature/new-api-endpoint

# Create Pull Request on GitHub

# After merge, update main
git checkout main
git pull
```

### Workflow 3: Bug Fix
```bash
# Create fix branch
git checkout -b fix/booking-error

# Fix the bug...

# Commit fix
git add .
git commit -m "fix: resolve booking not showing issue"

# Push and merge
git push -u origin fix/booking-error
```

---

## 🚫 .gitignore Important Files

Make sure these are in `.gitignore`:
```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
```

---

## 🆘 Troubleshooting

### Issue: "fatal: refusing to merge unrelated histories"
```bash
git pull origin main --allow-unrelated-histories
```

### Issue: Merge conflicts
```bash
# 1. Pull latest
git pull

# 2. Fix conflicts in files (look for <<<<<<, ======, >>>>>>)

# 3. Add resolved files
git add .

# 4. Complete merge
git commit -m "merge: resolve conflicts"

# 5. Push
git push
```

### Issue: Accidentally committed sensitive file
```bash
# Remove from git but keep locally
git rm --cached .env

# Add to .gitignore
echo ".env" >> .gitignore

# Commit removal
git commit -m "chore: remove sensitive file from git"
git push
```

### Issue: Wrong commit message
```bash
# Change last commit message
git commit --amend -m "new message"

# Force push (if already pushed)
git push --force
```

---

## 📊 Project History

### Initial Commit
```
Date: [Initial commit date]
Commit: 259eadc
Message: Initial commit
```

### Major Updates
- [ ] Initial project setup
- [ ] Email notification system
- [ ] API documentation
- [ ] Bug fixes (clinic bookings, dashboard)
- [ ] Admin profile menu removed

---

## 🎯 Best Practices

1. **Commit Often**: Small, focused commits are better
2. **Write Good Messages**: Describe what and why
3. **Pull Before Push**: Always sync with remote first
4. **Use Branches**: Don't work directly on main
5. **Review Before Commit**: Check `git status` and `git diff`
6. **Don't Commit Secrets**: Keep `.env` in `.gitignore`
7. **Test Before Push**: Make sure code works
8. **Keep Main Stable**: Use branches for experiments

---

## 📖 Quick Reference

| Command | Description |
|---------|-------------|
| `git status` | Check current status |
| `git add .` | Stage all changes |
| `git commit -m "msg"` | Commit with message |
| `git push` | Push to remote |
| `git pull` | Pull from remote |
| `git log` | View commit history |
| `git diff` | View changes |
| `git branch` | List branches |
| `git checkout -b name` | Create new branch |
| `git merge branch` | Merge branch |

---

**Last Updated:** June 8, 2026  
**Repository:** https://github.com/mahayasa78/VETRA-WEB.git
