# Workspace Rules

- **Frontend & UI Guidelines**: Before creating or modifying any frontend views, styling, or UI components, you MUST review and adhere to the guidelines specified in [UI Guidelines](file:///d:/Works/nor/projects/ffinsta/.agents/rules/ui.md). This ensures visual consistency, proper Tailwind usage, and correct RTL/Dark mode handling.

# Push Command Rule
When the user asks to 'push' or 'psuh', you MUST ALWAYS execute the following sequence sequentially: 
pm run build, then git add ., then git commit -m '<message>', and finally git push. Ensure that the build finishes before adding and committing so that frontend assets are included in the same commit.
