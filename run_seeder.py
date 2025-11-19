import subprocess
import os

os.chdir(r'c:\xampp\htdocs\CTFxD')
result = subprocess.run(['php', 'artisan', 'db:seed', '--class=MembersSeeder'], 
                       capture_output=True, text=True)
print(result.stdout)
if result.stderr:
    print(result.stderr)
