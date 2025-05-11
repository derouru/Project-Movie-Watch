import json
import os
import mysql.connector

def lambda_handler(event, context):
    try:
        # Get user_id from either format
        if 'queryStringParameters' in event and event['queryStringParameters']:
            user_id = int(event['queryStringParameters']['user_id'])
        elif 'user_id' in event:
            user_id = int(event['user_id'])
        else:
            raise ValueError("Missing user_id parameter")

        # Database connection and query
        connection = mysql.connector.connect(
            host=os.environ['RDS_HOST'],
            user=os.environ['USER_NAME'],
            password=os.environ['PASSWORD'],
            database=os.environ['DB_NAME']
        )
        cursor = connection.cursor()

        cursor.execute("SELECT * FROM movies WHERE user_id = %s", (user_id,))
        results = cursor.fetchall()

        table_data = [{
            "movie_id": row[0],
            "name": row[1],
            "watched": str(row[2])  # Convert datetime to string
        } for row in results]

        return {
            'statusCode': 200,
            'body': json.dumps(table_data)
        }

    except Exception as e:
        return {
            'statusCode': 500,
            'body': json.dumps({
                'error': str(e),
                'received_event': event  # For debugging
            })
        }
    finally:
        if 'connection' in locals():
            cursor.close()
            connection.close()