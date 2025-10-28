#!/usr/bin/env python3
import sys
import os
import json
from openai import OpenAI

API_KEY = os.getenv("OPENROUTER_API_KEY")

if not API_KEY:
    print("Error: Set OPENROUTER_API_KEY environment variable")
    sys.exit(1)

client = OpenAI(
    base_url="https://openrouter.ai/api/v1",
    api_key=API_KEY,
)

#MODEL = "anthropic/claude-3.5-sonnet"
MODEL = "deepseek/deepseek-r1-0528-qwen3-8b:free"

# Working directory
WORK_DIR = os.path.expanduser("~")

def read_file(filepath):
    """Read file content"""
    try:
        full_path = os.path.join(WORK_DIR, filepath)
        with open(full_path, 'r', encoding='utf-8') as f:
            return f.read()
    except Exception as e:
        return f"Error reading {filepath}: {str(e)}"

def write_file(filepath, content):
    """Write content to file"""
    try:
        full_path = os.path.join(WORK_DIR, filepath)
        os.makedirs(os.path.dirname(full_path), exist_ok=True)
        with open(full_path, 'w', encoding='utf-8') as f:
            f.write(content)
        return f"✓ File {filepath} saved successfully"
    except Exception as e:
        return f"Error writing {filepath}: {str(e)}"

def list_files(directory="."):
    """List files in directory"""
    try:
        full_path = os.path.join(WORK_DIR, directory)
        files = []
        for root, dirs, filenames in os.walk(full_path):
            dirs[:] = [d for d in dirs if not d.startswith('.')]
            for filename in filenames:
                if not filename.startswith('.'):
                    rel_path = os.path.relpath(os.path.join(root, filename), WORK_DIR)
                    files.append(rel_path)
        return "\n".join(files[:100])
    except Exception as e:
        return f"Error: {str(e)}"

def load_cursorrules():
    """Load .cursorrules file if exists"""
    cursorrules_path = os.path.join(WORK_DIR, ".cursorrules")
    if os.path.exists(cursorrules_path):
        try:
            with open(cursorrules_path, 'r', encoding='utf-8') as f:
                rules = f.read()
                return f"\n\n<project_rules>\n{rules}\n</project_rules>"
        except:
            return ""
    return ""

# System prompt with .cursorrules integration
def get_system_prompt():
    base_prompt = """You are an AI coding assistant for server-side development. Don’t tell which files I should edit, do it yourself, but before editing tell me what you’re going to change. The files you are editing are production ready. Providing abbreviated versions of files is strictly prohibited, as this will disrupt the website functionality. Always respond in English.

Available commands:
- READ_FILE: filepath - read file content
- WRITE_FILE: filepath | content - write to file
- LIST_FILES: directory - list files

Command format:
```command
WRITE_FILE: index.php
<?php
echo "Hello World";
?>
```

Guidelines:
- Always explain your actions before executing commands
- Use concise, efficient code
- Follow best practices for the language being used
- Be careful with file operations"""
    
    # Add .cursorrules if exists
    cursor_rules = load_cursorrules()
    return base_prompt + cursor_rules

messages = []

def init_conversation():
    """Initialize conversation with system prompt"""
    global messages
    messages = [{"role": "system", "content": get_system_prompt()}]

def parse_commands(response):
    """Parse commands from Claude's response"""
    commands = []
    lines = response.split('\n')
    in_command = False
    current_cmd = []
    
    for line in lines:
        if line.strip().startswith('```command'):
            in_command = True
            current_cmd = []
        elif line.strip() == '```' and in_command:
            in_command = False
            if current_cmd:
                commands.append('\n'.join(current_cmd))
        elif in_command:
            current_cmd.append(line)
    
    return commands

def execute_command(cmd):
    """Execute command"""
    if cmd.startswith('READ_FILE:'):
        filepath = cmd.replace('READ_FILE:', '').strip()
        return f"Content of {filepath}:\n```\n{read_file(filepath)}\n```"
    
    elif cmd.startswith('WRITE_FILE:'):
        parts = cmd.split('\n', 1)
        filepath = parts[0].replace('WRITE_FILE:', '').strip()
        content = parts[1] if len(parts) > 1 else ""
        return write_file(filepath, content)
    
    elif cmd.startswith('LIST_FILES:'):
        directory = cmd.replace('LIST_FILES:', '').strip() or "."
        return f"Files in {directory}:\n{list_files(directory)}"
    
    return "Unknown command"

def chat(query):
    messages.append({"role": "user", "content": query})
    
    try:
        completion = client.chat.completions.create(
            model=MODEL,
            messages=messages,
            temperature=0.7,
        )
        response = completion.choices[0].message.content
        messages.append({"role": "assistant", "content": response})
        
        # Print response
        print(response)
        
        # Parse and execute commands
        commands = parse_commands(response)
        if commands:
            print("\n--- Executing commands ---")
            for cmd in commands:
                result = execute_command(cmd)
                print(result)
                # Add result to context
                messages.append({"role": "user", "content": f"[Command result]: {result}"})
        
        return response
    except Exception as e:
        return f"Error: {str(e)}"

# Main logic
if __name__ == "__main__":
    init_conversation()
    
    # Check if .cursorrules exists
    if os.path.exists(os.path.join(WORK_DIR, ".cursorrules")):
        print("✓ Loaded .cursorrules")
    
    if len(sys.argv) > 1:
        query = " ".join(sys.argv[1:])
        chat(query)
    else:
        print(f"Claude Code Editor - Working directory: {WORK_DIR}")
        print("Quick commands: @read file, @write file, @list, @rules, exit\n")
        
        while True:
            try:
                query = input("> ")
                
                if query.lower() in ["exit", "quit", "q"]:
                    break
                
                # Quick commands
                if query.startswith('@read '):
                    filepath = query[6:].strip()
                    print(read_file(filepath))
                    continue
                elif query.startswith('@list'):
                    print(list_files())
                    continue
                elif query == '@rules':
                    rules_content = load_cursorrules()
                    if rules_content:
                        print(rules_content)
                    else:
                        print("No .cursorrules file found")
                    continue
                elif query.startswith('@write '):
                    # Quick write: @write filename.txt content here
                    parts = query[7:].split(None, 1)
                    if len(parts) == 2:
                        print(write_file(parts[0], parts[1]))
                    else:
                        print("Usage: @write filename content")
                    continue
                
                if query.strip():
                    chat(query)
                    print()
                    
            except (EOFError, KeyboardInterrupt):
                print("\nExiting...")
                break