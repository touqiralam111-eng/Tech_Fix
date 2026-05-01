# Fork & Pull Request Workflow Guide

This document demonstrates the complete fork and pull request workflow for contributing to the TechFix project on GitHub.

## 📚 What is a Fork?

A **fork** is a complete copy of a repository that you control. It allows you to:
- Make changes without affecting the original repository
- Experiment with new features safely
- Contribute to open-source projects
- Maintain your own version of a project

## 🔀 Fork Workflow Steps

### Step 1: Fork the Repository

1. Visit the original repository: [TechFix](https://github.com/touqiralam111-eng/Tech_Fix)
2. Click the **"Fork"** button in the top-right corner
3. Select where to fork it (usually your personal account)
4. GitHub creates a copy under your account: `your-username/Tech_Fix`

**Result**: You now have your own copy of the repository!

### Step 2: Clone Your Fork Locally

```bash
# Clone your fork (not the original)
git clone https://github.com/YOUR-USERNAME/Tech_Fix.git
cd Tech_Fix

# Configure the original repo as upstream
git remote add upstream https://github.com/touqiralam111-eng/Tech_Fix.git

# Verify remotes
git remote -v
# Output:
# origin     https://github.com/YOUR-USERNAME/Tech_Fix.git (fetch)
# origin     https://github.com/YOUR-USERNAME/Tech_Fix.git (push)
# upstream   https://github.com/touqiralam111-eng/Tech_Fix.git (fetch)
# upstream   https://github.com/touqiralam111-eng/Tech_Fix.git (push)
```

### Step 3: Create a Feature Branch

```bash
# Update master from upstream
git fetch upstream
git checkout master
git merge upstream/master

# Create a new branch for your changes
git checkout -b feature/my-improvement

# Example branch names:
# - feature/dark-mode
# - feature/multi-language-support
# - feature/performance-optimization
# - bugfix/fix-mobile-nav
```

### Step 4: Make Your Changes

```bash
# Edit files, add new features, etc.
# Example: Update the hero section styling

# Stage your changes
git add cssstyle.css index.php

# Commit with a meaningful message
git commit -m "feat: Add dark mode support for better UX

- Implemented CSS variables for dark theme
- Added toggle button for light/dark mode
- Updated all components for dark mode compatibility
- Tested on all major browsers"
```

### Step 5: Keep Your Fork Synchronized

```bash
# Before pushing, sync with upstream
git fetch upstream
git rebase upstream/master

# If there are conflicts, resolve them:
# 1. Fix conflicting files
git add <resolved-files>
git rebase --continue

# Or abort if needed
git rebase --abort
```

### Step 6: Push to Your Fork

```bash
# Push your branch to your fork
git push origin feature/my-improvement

# GitHub may suggest creating a PR - copy the link or:
# Go to https://github.com/YOUR-USERNAME/Tech_Fix
# You'll see a "Compare & pull request" button
```

### Step 7: Create a Pull Request

1. Navigate to your fork on GitHub
2. Click **"Pull requests"** tab
3. Click **"New pull request"**
4. Select:
   - **Base repository**: `touqiralam111-eng/Tech_Fix`
   - **Base branch**: `master`
   - **Head repository**: `YOUR-USERNAME/Tech_Fix`
   - **Compare branch**: `feature/my-improvement`

5. Fill in the PR template:

```markdown
## Description
Brief description of the changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Changes Made
- Point 1
- Point 2
- Point 3

## Testing Done
Description of tests performed

## Screenshots (if applicable)
Add screenshots for UI changes

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for clarity
- [ ] Documentation updated
- [ ] No new warnings generated
```

### Step 8: Code Review & Discussion

1. Project maintainers review your PR
2. They may request changes with comments
3. You can push additional commits to the same branch
4. Updates automatically appear in the PR
5. GitHub Actions runs automated tests

### Step 9: Merge Your PR

Once approved:
1. Click **"Squash and merge"** or **"Create a merge commit"**
2. Add a merge commit message if needed
3. Click **"Confirm merge"**
4. Delete your branch (GitHub offers this option)

### Step 10: Clean Up

```bash
# Delete the local branch
git branch -d feature/my-improvement

# Remove from your fork
git push origin --delete feature/my-improvement

# Sync master for next contribution
git fetch upstream
git checkout master
git merge upstream/master
git push origin master
```

## 📋 Example: Contributing Dark Mode Feature

### Complete Example Workflow

```bash
# Step 1: Fork on GitHub web interface

# Step 2: Clone and setup
git clone https://github.com/your-username/Tech_Fix.git
cd Tech_Fix
git remote add upstream https://github.com/touqiralam111-eng/Tech_Fix.git

# Step 3: Create feature branch
git checkout -b feature/dark-mode-support

# Step 4: Make changes
# Edit: cssstyle.css, index.php, js,script.js

# Step 5: Commit changes
git add .
git commit -m "feat: Implement dark mode theme support

- Added CSS custom properties for dark theme
- Created dark mode toggle switch
- Updated all components for dark mode
- Added localStorage persistence
- Tested on Chrome, Firefox, Safari"

# Step 6: Push to your fork
git push origin feature/dark-mode-support

# Step 7: Create PR on GitHub web interface
# - Go to your fork
# - Click "Compare & pull request"
# - Fill in description and changes
# - Submit PR

# Step 8: Respond to review comments
# - Make requested changes
# - Push updates: git push origin feature/dark-mode-support

# Step 9: PR gets merged
# - Maintainer clicks "Merge pull request"

# Step 10: Clean up
git branch -d feature/dark-mode-support
git push origin --delete feature/dark-mode-support
```

## 🔄 Syncing Your Fork Regularly

```bash
# Check for upstream updates
git fetch upstream
git status

# Merge upstream into your master
git checkout master
git merge upstream/master

# Keep your fork updated on GitHub
git push origin master

# For branches in development
git checkout feature/my-feature
git merge upstream/master
git push origin feature/my-feature
```

## ⚡ Best Practices

### 1. **Keep Your Fork Clean**
- Only make changes related to one feature per branch
- Delete merged branches
- Keep master synchronized with upstream

### 2. **Write Meaningful Commits**
```bash
# Good
git commit -m "feat: Add search functionality to services

- Implemented search input field
- Added filter logic for services
- Styled search results display"

# Bad
git commit -m "changes"
git commit -m "fix stuff"
```

### 3. **Frequent Small PRs Are Better**
- Easier to review
- Faster to merge
- Less chance of conflicts
- Better for CI/CD

### 4. **Always Sync Before Pushing**
```bash
git fetch upstream
git rebase upstream/master
# or
git merge upstream/master
```

### 5. **Use Branches for Everything**
- Never commit directly to master
- Even small changes deserve a branch
- Keeps history clean

### 6. **Write Good PR Descriptions**
- Explain why changes are needed
- Link related issues
- Show before/after for UI changes
- Include testing instructions

## 📊 Fork Workflow Diagram

```
Original Repo (upstream)
    ↓
    │ Fork
    ↓
Your Fork (origin)
    ↓
    │ Clone
    ↓
Local Repository
    ↓
    │ Create Branch
    ↓
feature/my-feature
    ↓
    │ Make Changes & Commit
    ↓
    │ Push to origin
    ↓
Your Fork (remote)
    ↓
    │ Create Pull Request
    ↓
Original Repo Pull Requests
    ↓
    │ Code Review
    ↓
    │ Merge
    ↓
Original Repo master
```

## 🆘 Common Issues & Solutions

### Issue: "Your branch has diverged"

```bash
# Solution: Rebase with upstream
git fetch upstream
git rebase upstream/master
```

### Issue: "Merge conflict"

```bash
# View conflicts
git status

# Edit conflicting files and resolve

# Mark as resolved
git add <file>
git commit -m "Resolve merge conflict"
```

### Issue: "Accidentally committed to master"

```bash
# Create new branch with current commits
git branch feature/my-feature

# Reset master to upstream
git checkout master
git reset --hard upstream/master
```

### Issue: "Need to update PR with latest master"

```bash
# Update your branch
git fetch upstream
git rebase upstream/master

# Force push (only for your fork!)
git push origin feature/my-feature --force
```

## 📚 Resources

- [GitHub Fork Documentation](https://docs.github.com/en/get-started/quickstart/fork-a-repo)
- [Creating Pull Requests](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request-from-a-fork)
- [Git Workflow Documentation](https://git-scm.com/book/en/v2/Git-Branching-Branching-Workflows)
- [Conventional Commits](https://www.conventionalcommits.org/)

## ✅ Verification Checklist

- [ ] Fork created on GitHub
- [ ] Fork cloned locally
- [ ] Upstream remote configured
- [ ] Feature branch created
- [ ] Changes made and tested
- [ ] Commits are meaningful
- [ ] Branch synced with upstream
- [ ] Changes pushed to fork
- [ ] Pull request created
- [ ] PR description is clear
- [ ] All tests passing
- [ ] Code review approved
- [ ] PR merged successfully
- [ ] Local branch deleted
- [ ] Remote branch deleted

---

**Ready to contribute?** Start by forking the repository and following the workflow above!
