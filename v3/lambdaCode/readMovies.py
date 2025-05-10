import json
import os
import mysql.connector

def lambda_handler(event, context):
    try:
        # take in the user_id parameter from the request
        user = int(event["queryStringParameters"]["user_id"])

        # connect to RDS database
        connection = mysql.connector.connect(
            host = os.environ['RDS_HOST'],
            user = os.environ['USER_NAME'],
            password = os.environ['PASSWORD'],
            database = os.environ['DB_NAME']
        )
        cursor = connection.cursor()

        # select query
        db_query = "SELECT * FROM movies WHERE user_id = %s"
        cursor.execute(db_query, (user,))
        results = cursor.fetchall()

        table_data = []
        for row in results:
            table_data.append({
                "movie_id": row[0],
                "name": row[1],
                "watched": row[2]
            })

        cursor.close()
        connection.close()

        return {
            'statusCode': 200,
            'body': json.dumps(table_data, indent=4, sort_keys=True, default=str)
        }
    except Exception as e:
        return {
            'statusCode': 500,
            'body': json.dumps({'error': str(e)})
        }
