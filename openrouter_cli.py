import sys
from openai import OpenAI

# Ваш API-ключ OpenRouter
API_KEY = "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"

# Инициализация клиента
client = OpenAI(
    base_url="https://openrouter.ai/api/v1",
    api_key=API_KEY,
)

# Проверка аргументов командной строки
if len(sys.argv) < 2:
    print("Использование: python openrouter_cli.py 'Ваш вопрос'")
    sys.exit(1)

# Получение запроса из аргументов
query = sys.argv[1]

# Отправка запроса к модели (например, Claude 3.5 Sonnet)
completion = client.chat.completions.create(
    model="anthropic/claude-3.5-sonnet",
    messages=[{"role": "user", "content": query}]
)

# Вывод ответа
print(completion.choices[0].message.content)
