# Contributing to TechFix

Thank you for your interest in contributing to TechFix! This document provides guidelines and instructions for contributing to the project.

## 🤝 Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on the code, not the person
- Help others learn and grow
- Report issues professionally

## 🚀 How to Contribute

### 1. Fork the Repository

Visit [TechFix Repository](https://github.com/touqiralam111-eng/Tech_Fix) and click the **Fork** button.

### 2. Clone Your Fork

```bash
git clone https://github.com/YOUR-USERNAME/Tech_Fix.git
cd Tech_Fix
git remote add upstream https://github.com/touqiralam111-eng/Tech_Fix.git
```

### 3. Create a Feature Branch

```bash
git checkout -b feature/your-feature-name
```

**Branch naming conventions**:
- `feature/` - New features
- `bugfix/` - Bug fixes
- `docs/` - Documentation updates
- `refactor/` - Code refactoring
- `test/` - Test additions

### 4. Make Your Changes

- Follow the code style of the project
- Keep commits focused and meaningful
- Write clear commit messages
- Test your changes thoroughly

### 5. Commit with Meaningful Messages

```bash
# Good commit message
git commit -m "feat: Add search functionality to services

- Implement search input and logic
- Create filter mechanism
- Add result display component
- Update styling for consistency"

# Format: <type>: <subject>
# Types: feat, fix, docs, style, refactor, test, chore
```

### 6. Push to Your Fork

```bash
git push origin feature/your-feature-name
```

### 7. Create a Pull Request

1. Go to your fork on GitHub
2. Click "Compare & pull request"
3. Fill in the PR template
4. Submit the PR

### 8. Respond to Review Comments

- Address feedback promptly
- Ask questions if feedback is unclear
- Make requested changes
- Push updates to the same branch

## 📝 Commit Message Guidelines

Follow the Conventional Commits specification:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### Types

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Changes that don't affect code meaning (formatting, etc)
- **refactor**: Code change that neither fixes a bug nor adds a feature
- **perf**: Code change that improves performance
- **test**: Adding missing or updating tests
- **chore**: Changes to build process, dependencies, etc

### Examples

```bash
# Feature
git commit -m "feat: Add dark mode support

- Implement CSS variables for themes
- Add theme toggle button
- Update all components"

# Bug Fix
git commit -m "fix: Correct mobile navigation layout

- Fix menu collapse issue on small screens
- Adjust padding for touch devices"

# Documentation
git commit -m "docs: Update installation instructions"

# Refactoring
git commit -m "refactor: Simplify form validation logic"
```

## 🎨 Code Style Guide

### HTML
- Use semantic HTML5 elements
- Proper indentation (2 spaces)
- Meaningful class and id names
- Validate with W3C

### CSS
- Use CSS variables for colors
- Mobile-first approach
- BEM naming convention for classes
- Organize by component/section

```css
/* Good */
.button-primary {
    background: var(--primary);
}

.button-primary:hover {
    background: var(--primary-dark);
}

/* Avoid */
.btn-blue {
    background: blue;
}

#specialbutton {
    color: red;
}
```

### JavaScript
- Use modern ES6+ features
- Clear, descriptive variable names
- Comments for complex logic
- DRY principle (Don't Repeat Yourself)
- Use const/let instead of var

```javascript
// Good
const formatPhoneNumber = (phone) => {
    return phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
};

// Less clear
var fPN = function(p) {
    return p.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
};
```

### PHP
- Follow PSR-12 coding standard
- Use meaningful variable names
- Add comments for complex logic
- Validate and sanitize inputs
- Use prepared statements for SQL

## 🧪 Testing

Before submitting:

1. Test your changes locally
2. Check responsive design on multiple devices
3. Verify no console errors
4. Test on different browsers if possible
5. Run any automated tests

## 📋 PR Template

```markdown
## Description
Brief description of the changes

## Related Issue
Fixes #(issue number)

## Type of Change
- [ ] Bug fix (non-breaking change fixing an issue)
- [ ] New feature (non-breaking change adding functionality)
- [ ] Breaking change (would cause existing functionality to change)
- [ ] Documentation update

## Changes Made
- Change 1
- Change 2
- Change 3

## Testing Done
Description of tests performed:
- [ ] Tested on desktop
- [ ] Tested on mobile
- [ ] Tested on tablet
- [ ] Verified responsiveness

## Screenshots (if applicable)
Add screenshots for UI changes

## Checklist
- [ ] My code follows the code style of this project
- [ ] I have updated the documentation accordingly
- [ ] I have added tests for my changes
- [ ] All new and existing tests passed
- [ ] My changes don't break any existing functionality
```

## 🐛 Reporting Bugs

### Before Reporting

- Check if the bug has been reported
- Search closed issues
- Test with the latest version

### When Reporting

Include:
- Browser and OS version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots if applicable

## 💡 Feature Requests

- Describe the feature clearly
- Explain the use case
- Provide examples
- Discuss potential implementation

## 📚 Project Structure

```
TechFix/
├── index.php              # Home page
├── cssstyle.css           # Main stylesheet
├── js,script.js           # JavaScript functionality
├── README.md              # Project documentation
├── CONTRIBUTING.md        # This file
├── FORK_WORKFLOW.md       # Fork workflow guide
├── .gitignore             # Git ignore rules
└── uploads/               # User uploads directory
```

## 🔄 Sync Your Fork

Keep your fork updated:

```bash
# Fetch from upstream
git fetch upstream

# Merge into your master
git checkout master
git merge upstream/master

# Push to your fork
git push origin master
```

## 🆘 Getting Help

- **Documentation**: Check [README.md](README.md)
- **Fork Guide**: See [FORK_WORKFLOW.md](FORK_WORKFLOW.md)
- **Issues**: Search open [issues](https://github.com/touqiralam111-eng/Tech_Fix/issues)
- **Discussions**: Use [GitHub Discussions](https://github.com/touqiralam111-eng/Tech_Fix/discussions)

## ✅ Contribution Checklist

Before submitting your PR:

- [ ] Code follows project style guide
- [ ] All tests pass
- [ ] Documentation is updated
- [ ] Commit messages are clear
- [ ] No unnecessary files committed
- [ ] Changes are minimal and focused
- [ ] No conflicts with master branch
- [ ] PR description is clear and complete

## 🎉 Thank You

Your contributions help make TechFix better for everyone. We appreciate your time and effort!

---

**Questions?** Feel free to [open an issue](https://github.com/touqiralam111-eng/Tech_Fix/issues/new) or ask in [discussions](https://github.com/touqiralam111-eng/Tech_Fix/discussions).
